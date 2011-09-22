<?php

interface ProjectSync
{
  function sync($local_dir, $remote_dir, $sync_opts = null);
}
