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

    public function __add(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other, bool $left): SimpleNumber|ComplexNumber
    {
        begin:
        {
            if ($other instanceof Real xor $self instanceof Real) {
                $result = $this->addXorReal($self, $other);
                goto end;
            }

            if ($other instanceof $self) {
                $result = $this->addSameType($self, $other);
                goto end;
            }

            if (is_numeric($other)) {
                $result = $this->addNumeric($self, $other);
                goto end;
            }

            if ($other instanceof Imaginary) {
                $result = new ComplexNumber($self, $other);
                goto end;
            }

            if ($other instanceof ComplexNumber) {
                $result = $other + $self;
                goto end;
            }

            throw new Exception('Invalid operator');

        }
        end:
        return $result;
    }

    private function addXorReal(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other): SimpleNumber|ComplexNumber
    {
        return $self instanceof Real
            ? new ComplexNumber($self, $other)
            : new ComplexNumber($other, $self);
    }

    private function addSameType(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other): SimpleNumber|ComplexNumber
    {
        return $other instanceof Real
            ? new Real($self->getValue() + $other->getValue())
            : new Imaginary($self->getValue() + $other->getValue());
    }
    private function addNumeric(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other): SimpleNumber|ComplexNumber
    {
        return $self instanceof Real
            ? new Real($self->getValue() + $other)
            : new ComplexNumber(new Real($other), $self);
    }

    public function __mul(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other): SimpleNumber|ComplexNumber
    {
        begin:
        {
            if ($other instanceof Real xor $self instanceof Real) {
                $result = new Imaginary($self->getValue() * $other->getValue());
                goto end;
            }

            if ($other instanceof Imaginary && $self instanceof Imaginary) {
                $result = new Real($self->getValue() * $other->getValue() * -1);
                goto end;
            }

            if ($other instanceof Real && $self instanceof Real) {
                $result = new Real($self->getValue() * $other->getValue());
                goto end;
            }

            if (is_numeric($other)) {
                $result = $this->multiplyNumeric($self, $other);
                goto end;
            }

            throw new Exception('Invalid operator');
        }
        end:
        return $result;
    }

    private function multiplyNumeric(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other): Real|Imaginary
    {
        $product = $self->getValue() * $other;
        return $self instanceof Real
            ? new Real($product)
            : new Imaginary($product);
    }

}