<?php
/**
 * Classe Action
 */
namespace iutnc\touiteur\action;

interface Action
{
    public function execute(): string;
}