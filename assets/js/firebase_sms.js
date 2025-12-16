firebase.auth().settings.appVerificationDisabledForTesting = true;

var phoneNumber = "+5939797146";
var testVerificationCode = "123456";

// This will render a fake reCAPTCHA as appVerificationDisabledForTesting is true.
// This will resolve after rendering without app verification.
var appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
// signInWithPhoneNumber will call appVerifier.verify() which will resolve with a fake
// reCAPTCHA response.
firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
    .then(function (confirmationResult) {
    	console.log(confirmationResult);
      // confirmationResult can resolve with the whitelisted testVerificationCode above.
      return confirmationResult.confirm(testVerificationCode)
    }).catch(function (error) {
      // Error; SMS not sent
      // ...
      console.log(error);
    });