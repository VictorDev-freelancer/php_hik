<?php

class Course

{

    public function __construct(

        protected string $title,

        protected string $subtitle,

        protected string $description,

        protected array $tags,

        protected Author $author

    ) {}

    public function __get($name)

    {

        if (property_exists($this, $name)) {

            return $this->$name;

        }

        return null;

    }

    public function __toString()

    {

        $html = "<h1>{$this->title}</h1>";

        $html .= "<h2>{$this->subtitle}</h2>";

        $html .= "<p>{$this->description}</p>";

        $html .= "<h3>Etiquetas:</h3>";

        $html .= "<ul>";

        foreach ($this->tags as $tag) {

            $html .= "<li>{$tag}</li>";

        }

        $html .= "</ul>";

        $html .= "<h3>Autor del curso:</h3>";

        $html .= $this->author;

        return $html;

    }

    public function addTags(string $tag): void

    {

        if (in_array($tag, $this->tags)) return;

        if (empty($tag)) return;

        if (count($this->tags) >= 5) return;

        if (strlen($tag) <= 3) return;

        $this->tags[] = $tag;

    }

    public function updateTitle(string $newTitle): void

    {

        if (empty($newTitle)) return;

        if (strlen($newTitle) < 5) return;

        $this->title = $newTitle;

    }

}