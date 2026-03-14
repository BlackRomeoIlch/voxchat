<?php

/** @var \Ilch\View $this */

header('Content-Type: application/json; charset=utf-8');
echo json_encode($this->get('json'));
