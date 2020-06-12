<?php
include("includes/includedFiles.php");

if (isset($_GET['term'])) {
    $term = urldecode($_GET['term']); // スペースが%20になるのを消す
} else {
    $term = "";
}

?>

<div class="searchContainer">
    <h4>Search for an artist, album or song</h4>
    <input type="text" class="searchInput" value="<?php echo $term; ?>" placeholder="Start typing..." onfocus="this.value = this.value">
</div>

<script>

  // ページリロード時にフォーカスする
  $(".searchInput").focus();

  $(function() {

      // キー入力が終了したら、タイマーを初期化して新しいタイマーをセット
      $(".searchInput").keyup(function() {
          clearTimeout(timer);

          timer = setTimeout(function() {
              let val = $(".searchInput").val();
              openPage("search.php?term=" + val);
          }, 2000);
      })

  })
</script>

<!-- 検索結果が空白ならページロードをストップ -->
<?php if ($term == "") exit(); ?>

<div class="tracklistContainer borderBottom">
  <h2>SONGS</h2>
  <ul class="tracklist">
    
    <?php
    $songsQuery = mysqli_query($con, "SELECT id FROM songs WHERE title LIKE '$term%' LIMIT 10");

    if (mysqli_num_rows($songsQuery) == 0) {
       echo "<span class='noResults'>" . $term ."に該当する曲は見つかりませんでした。</span>";
    }

    $songIdArray = array();

    $i = 1;
    while($row = mysqli_fetch_array($songsQuery)) {

      if ($i > 15) {
          break;
      }

      array_push($songIdArray, $row['id']);
       
      $albumSong = new Song($con, $row['id']);
      $albumArtist = $albumSong->getArtist();
      
      echo "<li class='tracklistRow'>
              <div class='trackCount'>
                  <img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
                  <span class='trackNumber'>$i</span>
              </div>

              <div class='trackInfo'>
                <span class='trackName'>" . $albumSong->getTitle() . "</span>
                <span class='artistName'>" . $albumArtist->getName() . "</span>
              </div>

              <div class='trackOptions'>
                <img class='optionsButton' src='assets/images/icons/more.png'>
              </div>

              <div class='trackDuration'>
                <span class='duration'>" . $albumSong->getDuration() . "</span>
              </div>
      
            </li>";
      $i++;
    }

    ?>

    <script>
      let tempSongIds = '<?php echo json_encode($songIdArray); ?>';
      tempPlaylist = JSON.parse(tempSongIds);
    </script>

  </ul>
</div>

<div class="artistsContainer borderBottom">
      <h2>ARTISTS</h2>

      <?php 
        $artistsQuery = mysqli_query($con, "SELECT id FROM artists WHERE name LIKE '$term%' LIMIT 10");

        if (mysqli_num_rows($artistsQuery) == 0) {
            echo "<span class='noResults'>" . $term ."に該当するアーティストは見つかりませんでした。</span>";
        }

        while($row = mysqli_fetch_array($artistsQuery)) {
            $artistFound = new Artist($con, $row['id']);

            echo "<div class='searchResultRow'>
                    <div class='artistName'>

                        <span role='link' tabindex='0' onclick='openPage(\"artist.php?id=" . $artistFound->getId() . "\")'>
                        "
                        . $artistFound->getName() .
                        "
                        </span>

                    </div>

                </div>";
        }
      ?>
</div>

<div class="gridViewContainer">
  <h2>ALBUMS</h2>
  <?php
        $albumQuery = mysqli_query($con, "SELECT * FROM albums WHERE title LIKE '$term%' LIMIT 10");

        if (mysqli_num_rows($albumQuery) == 0) {
          echo "<span class='noResults'>" . $term ."に該当するアルバムは見つかりませんでした。</span>";
        }

        while($row = mysqli_fetch_array($albumQuery)) {

          echo "<div class='gridViewItem'>
                    <span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
                        <img src='" . $row['artworkPath'] . "'>

                        <div class='gridViewInfo'>"
                            . $row['title'] .
                        "</div>
                    </span>
                </div>";

        }
    ?>
</div>