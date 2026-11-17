<?php

namespace App\Exceptions;

use Exception;

// tanda "aturan bisnis pesanan dilanggar" (stok kurang, status loncat, dst).
// dipisah dari error sistem biar controller bisa nampilin pesannya ke kasir
class OrderException extends Exception
{
}