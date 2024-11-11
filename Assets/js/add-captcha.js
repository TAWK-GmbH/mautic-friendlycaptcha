function addCaptcha(wrapper, inputName, siteKey, version) {
    if (version == 'v1') {
        const myCustomWidget = new friendlyChallenge.WidgetInstance(wrapper, {
            sitekey: siteKey,
            startMode: 'auto',
            solutionFieldName: inputName
        });

        return;
    } 

    const myCustomWidget = new frcaptcha.createWidget({
        element: wrapper,
        sitekey: siteKey,
        startMode: 'auto',
        solutionFieldName: inputName
    });
}
/**
 * 
 * @param {string} wrapperId Friendly Captcha form wrapper id
 * @param {string} inputName Name of the friendly captcha input
 * @param {string} siteKey Friendly Captcha site key
 * @param {string} siteKey Friendly Captcha site key
 * @param {string} version Friendly Captcha api version
 */
function scheduleAddCaptcha(wrapperId, inputName, siteKey, version) {
    const wrapper = document.querySelector(`#${wrapperId}`);
    if (!wrapper) {
        throw new Error(`No friendly captcha wrapper element found with id '${wrapperId}'`);
    }

    if (friendlyChallenge) {
        addCaptcha(wrapper, inputName, siteKey, version)
        return;
    }

    const timer = setInterval(function () {
        if (friendlyChallenge) {
            addCaptcha(wrapper, inputName, siteKey, version);
            clearInterval(timer);
        }    
        
    }, 5000);
};