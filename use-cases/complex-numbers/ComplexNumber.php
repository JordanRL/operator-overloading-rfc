<?php

namespace Examples\ComplexNumbers;

class ComplexNumber
{

    private Real      $realPart;
    private Imaginary $imaginaryPart;

    public function __construct(Real $realPart, Imaginary $imaginaryPart)
    {
        $this->realPart      = $realPart;
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
        begin:
        {
            [$realPart, $imaginaryPart] = $this->addParts($other);

            if (0 === $realPart->abs()) {
                $result = new Imaginary($imaginaryPart);
                goto end;
            }

            if (0 === $imaginaryPart->abs()) {
                $result = new Real($realPart);
                goto end;
            }

            $result = new ComplexNumber($realPart, $imaginaryPart);
        }
        end:
        return $result;
    }

    public function __mul(ComplexNumber $self, SimpleNumber|ComplexNumber|int|float $other, bool $left): Imaginary|Real|ComplexNumber
    {
        begin:
        {

            [$realPart,$imaginaryPart] = $this->multiplyParts($other);


            if (0 === $realPart->abs() ) {
                $result = new Imaginary($imaginaryPart);
                goto end;
            }
            if (0 === $imaginaryPart->abs() ) {
                $result = new Real($realPart);
                goto end;
            }
            $result = new ComplexNumber($realPart, $imaginaryPart);

        }
        end:
        return $result;

    }

    private function addParts(Imaginary|Real|ComplexNumber|int|float $other): array
    {
        begin:
        {
            $realPart      = $this->realPart;
            $imaginaryPart = $this->imaginaryPart;

            if ($other instanceof ComplexNumber) {
                $realPart      += $other->getRealPart();
                $imaginaryPart += $other->getImaginaryPart();
                goto end;
            }

            if (is_int($other)) {
                $realPart += $other;
                goto end;
            }

            if (is_float($other)) {
                $realPart += $other;
                goto end;
            }

            if ($other instanceof Real) {
                $realPart += $other;
                goto end;
            }

            $imaginaryPart += $other;
        }
        end:
        return [$realPart, $imaginaryPart];
    }

    private function multiplyRealPart(SimpleNumber|ComplexNumber|int|float $other): Real
    {
        return $this->realPart * $other->getRealPart()
        + $this->imaginaryPart * $other->getImaginaryPart();
    }

    private function multiplyImaginaryPart(SimpleNumber|ComplexNumber|int|float $other): Imaginary
    {
        return $this->realPart * $other->getImaginaryPart()
        + $this->imaginaryPart * $other->getRealPart();
    }

    private function multiplyParts(Imaginary|Real|ComplexNumber|int|float $other): array
    {
        begin:
        {
            if ($other instanceof ComplexNumber) {
                $realPart      = $this->multiplyRealPart($other);
                $imaginaryPart = $this->multiplyImaginaryPart($other);
                goto end;
            }

            if (is_int($other)) {
                $realPart      = $this->realPart * $other;
                $imaginaryPart = $this->imaginaryPart * $other;
                goto end;
            }

            if (is_float($other)) {
                $realPart      = $this->realPart * $other;
                $imaginaryPart = $this->imaginaryPart * $other;
                goto end;
            }

            if ($other instanceof Real) {
                $realPart      = $this->realPart * $other;
                $imaginaryPart = $this->imaginaryPart * $other;
                goto end;
            }

            $realPart      = $this->imaginaryPart * $other;
            $imaginaryPart = $this->realPart * $other;
        }
        end:
        return [$realPart, $imaginaryPart];
    }

}