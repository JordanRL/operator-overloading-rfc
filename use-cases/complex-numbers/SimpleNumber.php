<?php

namespace Examples\ComplexNumbers;

use Exception;

abstract class SimpleNumber
{
    private float $value;

    public function __construct(int|float $value)
    {
        $this->value = (float)$value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function abs()
    {
        return abs($this->value);
    }

    public function __add(Number $other, bool $left): Number
    {
        if ($other instanceof Real xor $this instanceof Real) {
            $result = $this->addXorReal($other);
            goto end;
        }

        if ($other instanceof $this) {
            $result = $this->addSameType($other);
            goto end;
        }

        if (is_numeric($other)) {
            $result = $this->addNumeric($other);
            goto end;
        }

        if ($other instanceof Imaginary) {
            $result = new ComplexNumber($other);
            goto end;
        }

        if ($other instanceof ComplexNumber) {
            $result = $other + $this;
            goto end;
        }

        throw new Exception('Invalid operator');

    end:

        return $result;
    }

    private function addXorReal(Number $other): Number
    {
        return $this instanceof Real
            ? new ComplexNumber($this, $other)
            : new ComplexNumber($other, $this);
    }

    private function addSameType(Number $other): Number
    {
        return $other instanceof Real
            ? new Real($this->getValue() + $other->getValue())
            : new Imaginary($this->getValue() + $other->getValue());
    }
    private function addNumeric(Number $other): Number
    {
        return $this instanceof Real
            ? new Real($this->getValue() + $other)
            : new ComplexNumber(new Real($other), $this);
    }

    public function __mul(SimpleNumber $this, Number $other): Number
    {

        if ($this->xorReal($other,$this)) {
            $result = new Imaginary($this->getValue() * $other->getValue());
            goto end;
        }

        if ($this->allImaginary($other, $this)) {
            $result = new Real($this->getValue() * $other->getValue() * -1);
            goto end;
        }

        if ($this->allReal($other, $this)) {
            $result = new Real($this->getValue() * $other->getValue());
            goto end;
        }

        if (is_numeric($other)) {
            $result = $this->multiplyNumeric($other);
            goto end;
        }

        throw new Exception('Invalid operator');

    end:
        return $result;
    }

    private function xorReal(Number $number1,$number2): bool
    {
        return $number1 instanceof Real xor $number2 instanceof Real;
    }

    private function allReal(Number ...$number): bool
    {
        $all_real = true;
        foreach( $number as $_number) {
            if ( $_number instanceof Real ) {
                continue;
            }
            $all_real = false;
            break;
        }
        return $all_real;
    }

    private function allImaginary(Number ...$number): bool
    {
        $all_imaginary = true;
        foreach( $number as $_number) {
            if ( $_number instanceof Imaginary ) {
                continue;
            }
            $all_imaginary = false;
            break;
        }
        return $all_imaginary;
    }

    private function multiplyNumeric(Number $other): Real|Imaginary
    {
        $product = $this->getValue() * $other;
        return $this instanceof Real
            ? new Real($product)
            : new Imaginary($product);
    }

}