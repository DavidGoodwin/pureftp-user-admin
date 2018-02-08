<?php

namespace PureFTPAdmin\Form;

interface Form {

    public function __construct(array $data = []);

   public  function isValid(array $data);

    public function render();

    public function getValues();
}
