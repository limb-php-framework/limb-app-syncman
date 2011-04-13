<?php

interface Repository
{
  function getPath();
  function getType();
  function getFetchProjectCmd($wc_path);
  function getUpdateCmd($wc_path, $ignore_externals = false);
  function getDiffCmd($wc_path, $revision_wc, $resivion_remote);
  function getLogCmd($wc_path, $revision_wc, $resivion_remote);
  function getLastCommitCmd($wc_path, $is_remote = false);
}
