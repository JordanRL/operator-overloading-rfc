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

    public function __add(Number $other): Number
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

    end:

        return $result;
    }

    public function __mul(Number $other): Number
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

    end:

        return $result;

    }

    private function addParts(Number $other): array
    {
        $realPart      = $this->realPart;
        $imaginaryPart = $this->imaginaryPart;

        if ($other instanceof ComplexNumber) {
            $realPart      += $other->getRealPart();
            $imaginaryPart += $other->getImaginaryPart();
            goto end;
        }

        if ($this->isRealNumber($other)) {
            $realPart += $other;
            goto end;
        }

        $imaginaryPart += $other;

    end:

        return [$realPart, $imaginaryPart];
    }

    private function isRealNumber(Number $other): bool {

        $is_real_number = true;

        if (is_int($other)) {
            goto end;
        }

        if (is_float($other)) {
            goto end;
        }

        if ($other instanceof Real) {
            goto end;
        }

        $is_real_number = false;

    end:

        return $is_real_number;

    }

    private function multiplyRealPart(Number $other): Real
    {
        return $this->realPart * $other->getRealPart()
        + $this->imaginaryPart * $other->getImaginaryPart();
    }

    private function multiplyImaginaryPart(Number $other): Imaginary
    {
        return $this->realPart * $other->getImaginaryPart()
        + $this->imaginaryPart * $other->getRealPart();
    }

    private function multiplyParts(Number $other): array
    {
        if ($other instanceof ComplexNumber) {
            $realPart      = $this->multiplyRealPart($other);
            $imaginaryPart = $this->multiplyImaginaryPart($other);
            goto end;
        }

        if ($this->isRealNumber($other)) {
            $realPart      = $this->realPart * $other;
            $imaginaryPart = $this->imaginaryPart * $other;
            goto end;
        }

        $realPart      = $this->imaginaryPart * $other;
        $imaginaryPart = $this->realPart * $other;

    end:

        return [$realPart, $imaginaryPart];
    }

}