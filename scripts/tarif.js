var myLanguage = {
      errorTitle : 'Erreur lors de la soumission du formulaire !',
      requiredFields : 'Tous les champs requis ne sont pas renseignés',
      badTime : 'Vous n\'avez pas fourni un heure correcte',
      badEmail : 'L\'adresse e-mail fournie n\'est pas correcte',
      badTelephone : 'Le numéro de télphone fourni n\'est pas correcte',
      badSecurityAnswer : 'Vous avez mal répondu à la question de sécurité',
      badDate : 'La date fournie n\'est pas correcte',
      lengthBadStart : 'Vous devez donner une réponse entre ',
      lengthBadEnd : ' caractères',
      lengthTooLongStart : 'Vous avez donné une réponse plus grande que ',
      lengthTooShortStart : 'Vous avez donné une réponse plus petite que ',
      notConfirmed : 'Les valeurs ne peuvent être confirmées',
      badDomain : 'Domaine incorrecte',
      badUrl : 'URL incorrecte',
      badCustomVal : 'Vous avez donné une mauvaise réponse',
      badInt : 'La réponse donnée n\'est pas un nombre correcte',
      badSecurityNumber : 'Le numéro de sécurité social est incorrect',
      badUKVatAnswer : 'Incorrect UK VAT Number',
      badStrength : 'Le mot de passe n\'est pas assez fort',
      badNumberOfSelectedOptionsStart : 'Vous devez choisir au moins ',
      badNumberOfSelectedOptionsEnd : ' réponses',
      badAlphaNumeric : 'La réponse ne doit contenir que des caractères alphanumériques ',
      badAlphaNumericExtra: ' et ',
      wrongFileSize : 'Le fichier que vous essayez d\'envoyer est trop volumineux',
      wrongFileType : 'Le fichier que vous essayez d\'envoyer n\'est pas du bon type',
      groupCheckedRangeStart : 'Merci de choisir entre ',
      groupCheckedTooFewStart : 'Merci de choisir au moins ',
      groupCheckedTooManyStart : 'Merci de choisir un maximum de ',
      groupCheckedEnd : ' objet(s)'
    };

  $.validate({
    language : myLanguage
  });