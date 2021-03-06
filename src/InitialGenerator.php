<?php

namespace Laravolt\Avatar;

use Illuminate\Support\Collection;
use Stringy\Stringy;

class InitialGenerator
{
    protected $name = '';

    protected $length = 2;

    protected $ascii = false;

    /**
     * Identifier constructor.
     *
     * @param string $name
     * @param int    $length
     */
    public function __construct($name = '', $length = 2)
    {
        $this->setName($name);
        $this->length = $length;
    }

    public function setName($name)
    {
        if (is_array($name)) {
            throw new \InvalidArgumentException(
                'Passed value cannot be an array'
            );
        } elseif (is_object($name) && !method_exists($name, '__toString')) {
            throw new \InvalidArgumentException(
                'Passed object must have a __toString method'
            );
        }

        $name = Stringy::create($name)->collapseWhitespace();

        if ($this->ascii) {
            $name = $name->toAscii();
        }

        $this->name = $name;

        return $this;
    }

    public function setLength($length = 2)
    {
        $this->length = $length;

        return $this;
    }

    public function setAscii($ascii)
    {
        $this->ascii = $ascii;

        if ($this->ascii) {
            $this->name = $this->name->toAscii();
        }

        return $this;
    }

    public function getInitial()
    {
        $words = new Collection(explode(' ', $this->name));

        // if name contains single word, use first N character
        if ($words->count() === 1) {
            if ($this->name->length() >= $this->length) {
                return $this->name->substr(0, $this->length);
            }

            return (string) $words->first();
        }

        // otherwise, use initial char from each word
        $initials = new Collection();
        $words->each(function ($word) use ($initials) {
            $initials->push(Stringy::create($word)->substr(0, 1));
        });

        return $initials->slice(0, $this->length)->implode('');
    }
}
