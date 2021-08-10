<?php

namespace Examples\ComplexNumbers;

class ComplexNumber
{

    private Real $realPart;
    private Imaginary $imaginaryPart;

    public function __construct(Real $realPart, Imaginary $imaginaryPart)
    {
        $this->realPart = $realPart;
        $this->imaginaryPart = $imaginaryPart;
    }

    public function getRealPart(): Real
    {
        return $this->realPart;
    }

    public function getImaginaryPart(): Imaginary
    {
        return $this->imaginaryPart;
    }

    public function __add(ComplexNumber $self, Imaginary|Real|ComplexNumber|int|float $other, bool $left): Imaginary|Real|ComplexNumber
    {
        $realPart = $this->realPart;
        $imaginaryPart = $this->imaginaryPart;

        if ($other instanceof ComplexNumber) {
            $realPart += $other->getRealPart();
            $imaginaryPart += $other->getImaginaryPart();
        } else {
            if (is_int($other) || is_float($other) || $other instanceof Real) {
                $realPart += $other;
            } else {
                $imaginaryPart += $other;
            }
        }

        if ($realPart->abs() != 0 && $imaginaryPart->abs() != 0) {
            return new ComplexNumber($realPart, $imaginaryPart);
        }

        return ($realPart == 0) ? new Imaginary($imaginaryPart) : new Real($realPart);
    }

    public function __mul(ComplexNumber $self, SimpleNumber|ComplexNumber|int|float $other, bool $left): Imaginary|Real|ComplexNumber
    {
        if ($other instanceof ComplexNumber) {
            $realPart = ($this->imaginaryPart * $other->getImaginaryPart()) + ($this->realPart * $other->getRealPart());
            $imaginaryPart = ($this->realPart * $other->getImaginaryPart()) + ($this->imaginaryPart * $other->getRealPart());
        } else {
            if (is_int($other) || is_float($other) || $other instanceof Real) {
                $realPart = $this->realPart * $other;
                $imaginaryPart = $this->imaginaryPart * $other;
            } else {
                $realPart = $this->imaginaryPart * $other;
                $imaginaryPart = $this->realPart * $other;
            }
        }

        if ($realPart->abs() != 0 && $imaginaryPart->abs() != 0) {
            return new ComplexNumber($realPart, $imaginaryPart);
        }

        return ($realPart == 0) ? new Imaginary($imaginaryPart) : new Real($realPart);
    }

}