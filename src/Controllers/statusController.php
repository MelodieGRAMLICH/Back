<?php
class StatusController
{
    public static function home()
    {
        Flight::json([
            'message' => 'API DreamOC'
        ]);
    }

    public static function status()
    {
        Flight::json([
            'status' => 'ok',
            'api' => 'DreamOC'
        ]);
    }
}