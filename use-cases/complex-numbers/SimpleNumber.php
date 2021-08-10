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

    public function __add(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other, bool $left): SimpleNumber|ComplexNumber
    {
        if ($other instanceof Real xor $self instanceof Real) {
            return ($self instanceof Real ? new ComplexNumber($self, $other) : new ComplexNumber($other, $self));
        }

        if ($other instanceof $self) {
            if ($other instanceof Real) {
                return new Real($self->getValue() + $other->getValue());
            } else {
                return new Imaginary($self->getValue() + $other->getValue());
            }
        } elseif (is_numeric($other)) {
            if ($self instanceof Real) {
                return new Real($self->getValue() + $other);
            } else {
                return new ComplexNumber(new Real($other), $self);
            }
        } elseif ($other instanceof Imaginary) {
            return new ComplexNumber($self, $other);
        } elseif ($other instanceof ComplexNumber) {
            return $other + $self;
        } else {
            throw new Exception('Invalid operator');
        }
    }

    public function __mul(SimpleNumber $self, SimpleNumber|ComplexNumber|int|float $other, bool $left): SimpleNumber|ComplexNumber
    {
        if ($other instanceof Real xor $self instanceof Real) {
            return new Imaginary($self->getValue() * $other->getValue());
        } elseif ($other instanceof Imaginary && $self instanceof Imaginary) {
            return new Real($self->getValue() * $other->getValue() * -1);
        } elseif ($other instanceof Real && $self instanceof Real) {
            return new Real($self->getValue() * $other->getValue());
        } elseif (is_numeric($other)) {
            return ($self instanceof Real ? new Real($self->getValue() * $other) : new Imaginary($self->getValue() * $other));
        } else {
            throw new Exception('Invalid operator');
        }
    }

}