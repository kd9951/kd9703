<?php
namespace Kd9703\Framework\StrictInvokator\Exceptions;

/**
 * 外部メディアのコールが結果的に不要な呼び出しだった
 * 内部的には何も変化がないので致命的なエラーではないが、
 * APIコールは発生しているので、数が多いならなくしたほうが良い
 * WARNINGレベル
 */
class UnnecessaryCallException extends StrictInvokatorException
{
}
