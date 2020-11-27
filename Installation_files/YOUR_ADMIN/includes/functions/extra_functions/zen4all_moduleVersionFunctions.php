<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @param string $repository
 * @return string <p>Latest version of a module</p>
 */
function getLatestVersion(string $repository)
{
  $url = 'https://api.github.com/repos/Zen4All-nl/' . $repository . '/git/refs/tags';
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, "test");
  $r = curl_exec($ch);
  curl_close($ch);

  $response_array = json_decode($r, true);
  $lastInstantInArray = end($response_array);
  $latestVersion = preg_replace('/refs\/tags\//', '', $lastInstantInArray['ref']);
  return $latestVersion;
}

/**
 * 
 * @param string $repository Name of the GitHub repository
 * @param string $currentVersion The current module version
 * @return boolean
 */
function updatAvailable(string $repository, string $currentVersion)
{

  $latestVersion = getLatestVersion($repository, $currentVersion);
  $update = 'false';
  if (version_compare($latestVersion, $currentVersion) == 1) {
    $update = 'true';
  }
  return $update;
}
