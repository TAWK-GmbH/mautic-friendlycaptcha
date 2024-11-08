function addCaptcha(wrapper, inputName, siteKey) {
    const myCustomWidget = new friendlyChallenge.WidgetInstance(wrapper, {
        sitekey: siteKey,
        startMode: 'auto',
        solutionFieldName: inputName
    });
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