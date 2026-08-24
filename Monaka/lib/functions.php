<?php

function h($s) {
  return htmlspecialchars((string) $s, ENT_QUOTES, "UTF-8");
}
