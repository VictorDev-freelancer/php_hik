<?php
    class Course
    {
        public function __construct(
            protected string $titulo,
            protected string $subtitulo,
            protected string $contenido,
            protected array $lenguaje
        ){
            #Codigo constructor
        }
        
        public function obtenerTitulo() :string
        {
            return $this->titulo;
        }
        public function obtenersubtitulo() :string
        {
            return $this->subtitulo;
        }
        public function obtenercontenido() :string
        {
            return $this->contenido;
        }
        public function obtenerLenguaje() :array
        {
            return $this->lenguaje;
        }
        public function agregarLenguaje($lenguaje) :void
        {
            $this->lenguaje[] = $lenguaje;
        }
        public function actualizarTitulo($titulo) :void
        {
            $this->titulo = $titulo;
        }
    }
?>