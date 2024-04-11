<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

class ErrorManager
{


    public function isValidDateFormat(string $dateString, string $expectedFormat)
    {
        $date = \DateTime::createFromFormat($expectedFormat, $dateString);
        if ($date === false || $date->format($expectedFormat) !== $dateString) {
            throw new Exception(ErrorTypes::INVALID_DATE_FORMAT);
        }
    }
    public function isValidPassword(string $password)
    {
        if (!(strlen($password) >= 8 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[!@#$%^&*()-_+=]/', $password))) {
            throw new Exception(ErrorTypes::INVALID_PASSWORD_FORMAT);
        }
    }
    public function checkRequiredAttributes(array $data, array $requiredAttributes)
    {
        foreach ($requiredAttributes as $attribute) {
            if (!isset($data[$attribute])) {
                throw new Exception(ErrorTypes::MISSING_ATTRIBUTES);
            }
        }
    }
    public  function isAgeValid(string $dateOfBirth, int $minimumAge)
    {
        $today = new \DateTime();
        $birthdate = new \DateTime($dateOfBirth);
        $age = $today->diff($birthdate)->y;

        if ($age < $minimumAge) {
            throw new Exception(ErrorTypes::INVALID_AGE, $minimumAge);
        }
    }

    public  function isValidPhoneNumber(string $phoneNumber)
    {
        if (!preg_match('/^0[1-9]([-. ]?[0-9]{2}){4}$/', $phoneNumber)) {
            throw new Exception(ErrorTypes::INVALID_PHONE_NUMBER);
        }
    }

    public  function isValidGender(string $gender)
    {

        if (!in_array($gender, [0, 1])) {

            throw new Exception(ErrorTypes::INVALID_GENDER);
        }
    }

    public function generateError(string $errorType, string $variable = null): JsonResponse
    {
        $errorMessage = '';
        $codeErreur = '';

        switch ($errorType) {
            case 'TooManyAttempts':
                $errorMessage = "Trop de tentatives de connexion (5 max). Veuillez réessayer ultérieurement - $variable minutes restantes";
                $codeErreur = 429;
                break;
            case 'MissingAttributes':
                $errorMessage = 'Une ou plusieurs données obligatoires sont manquantes';
                $codeErreur = 400;
                break;
            case 'MissingAttributesLogin':
                $errorMessage = 'Email/Password manquants';
                $codeErreur = 400;
                break;
            case 'InvalidEmail':
                $errorMessage = "Le format de l'email est invalide.";
                $codeErreur = 400;
                break;

            case 'InvalidDateFormat':
                $errorMessage = "Le format de la date de naissance est invalide.Le format attendu est JJ/MM/AAAA";
                $codeErreur = 400;
                break;
            case 'InvalidAge':
                $errorMessage = "L'utilisateur doit avoir au moins $variable ans";
                $codeErreur = 400;
                break;
            case 'InvalidPasswordFormat':
                $errorMessage = "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et avoir 8 caractères minimum.";
                $codeErreur = 400;
                break;
            case 'UserNotFound':
                $errorMessage = 'Aucun utilisateur trouvé. Mot de passe ou Identifiant incorrect';
                $codeErreur = 400;
                break;
            case 'AccountNotActive':
                $errorMessage = "Le compte n'est plus actif ou est suspendu.";
                $codeErreur = 403;
                break;
            case 'UnexpectedError':
                $errorMessage = 'Une erreur inattendue s\'est produite.';
                $codeErreur = 400;
                break;
            case 'InvalidPhoneNumber':
                $errorMessage = 'Le format du numéro de téléphone est invalide.';
                $codeErreur = 400;
                break;
            case 'InvalidGender':
                $errorMessage = 'La valeur du champ sexe est invalide.Les valeurs autorisées sont 0 pour Femme,1 pour Homme.';
                $codeErreur = 400;
                break;
            case 'NotUniqueEmail':
                $errorMessage = 'Cet email est déja utilisé par un autre compte.';
                $codeErreur = 409;
                break;
            default:
                $errorMessage = 'Erreur inconnue';
                $codeErreur = 400;
                break;
        }

        return new JsonResponse([
            'error' => true,
            'message' => $errorMessage,
        ], $codeErreur);
    }
}
