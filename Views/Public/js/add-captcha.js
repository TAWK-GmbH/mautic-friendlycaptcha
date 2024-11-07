function addCaptcha(wrapper, inputName, siteKey) {
    const myCustomWidget = new friendlyChallenge.WidgetInstance(wrapper, {
        sitekey: siteKey,
        startMode: 'auto',
        solutionFieldName: inputName
    });

    const captchaInput = myCustomWidget.e.children[2];
    if (captchaInput) {
        console.log(captchaInput);
        captchaInput.id = "mauticform_input_test_t";
    }
}
/**
 * 
 * @param {string} formId Friendly Captcha form wrapper
 * @param {string} siteKey Friendly Captcha site key
 */
function scheduleAddCaptcha(wrapperId, inputName, siteKey) {
    const wrapper = document.querySelector(`#${wrapperId}`);
    if (!wrapper) {
        throw new Error(`No friendly captcha wrapper element found with id '${wrapperId}'`);
    }

    if (friendlyChallenge) {
        addCaptcha(wrapper, inputName, siteKey)
        return;
    }

    const timer = setInterval(function () {
        if (friendlyChallenge) {
            addCaptcha(wrapper, inputName, siteKey);
            clearInterval(timer);
        }    
        
    }, 5000);
};