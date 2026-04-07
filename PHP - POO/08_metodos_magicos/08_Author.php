<?php

class Author

{

    public function __construct(

        protected string $name,

        protected string $email,

        protected string $bio

    ) {}

    public function __get($property)

    {

        if (property_exists($this, $property)) {

            return $this->$property;

        }

        return null;

    }

    public function __toString()

    {

        $html = "<h3>Autor: {$this->name}</h3>";

        $html .= "<p><strong>Email:</strong> {$this->email}</p>";

        $html .= "<p>{$this->bio}</p>";

        return $html;

    }

    public function updateName(string $newName): void

    {

        if (empty($newName)) return;

        if (strlen($newName) < 3) return;

        $this->name = $newName;

    }

}