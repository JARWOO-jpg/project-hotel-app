<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception ketika kamar tidak tersedia (sudah diambil user lain).
 * Digunakan di dalam DB::transaction() agar otomatis rollback.
 */
class RoomUnavailableException extends RuntimeException
{
    //
}
