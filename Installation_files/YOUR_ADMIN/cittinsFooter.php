<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="panel-footer">
  <?php
  if (!isset($_SESSION['Zen4All']['updateAvailableCittins']) || empty($_SESSION['Zen4All']['updateAvailableCittins'])) {
    $updateAvailable = updatAvailable(CITTINS_REPOSITORY_NAME, ZEN4ALL_CITTINS_VERSION);
    $_SESSION['Zen4All']['updateAvailableCittins'] = $updateAvailable;
  }
  ?>
  <div class="row text-center">
    <strong>Cittins is developed by <a href="https://zen4all.nl" title="Zen4All" target="_blank">Zen4All</a>.</strong> - Installed Version: <?php echo ZEN4ALL_CITTINS_VERSION; ?>
    <?php if ($_SESSION['Zen4All']['updateAvailableCittins'] == 'true') { ?>
      - <i class="fa fa-github fa-lg"></i> A <a href="https://github.com/Zen4All-nl/Zen-Cart-Collect-Info-Through-Tabs-In-New-Style/releases/latest" target="_blank">new version</a> (<?php echo getLatestVersion(CITTINS_REPOSITORY_NAME); ?>) is available at GitHub.
    <?php } ?>
  </div>
  <div class="row text-center">
    <img src="images/zen4all_logo_small.png" alt="Zen4All Logo" title="Zen4All Logo"> Copyright  &COPY; 2008-<?php echo date("Y"); ?> Zen4All
  </div>
</div>