<?php
abstract class BaseController
{
    public function __construct()
    {
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            header('Location: '.url('?action=login'));
            exit;
        }
    }
}
