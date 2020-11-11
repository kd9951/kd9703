<?php
namespace Kd9703\Framework\StrictInvokator;

/**
 * Usecase や MediaAccess のための __invoke の入出力チェックを厳格にするためのもの
 */
interface StrictInvokatorInterface
{
    public function __invoke(array $request = []);
}
