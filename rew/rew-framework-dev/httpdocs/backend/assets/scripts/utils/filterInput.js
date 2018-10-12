/**
 * Filter input element characters
 * based on pattern attribute (`<input pattern="" />`)
 * @param {object} evt - The `Event` object
 */

export default (evt) => {
    let text = null;
    const input = evt.target;
    if (evt.type === 'keypress') {
        text = evt.key || String.fromCharCode(evt.which);
    }
    const legalCharacters = input.getAttribute('pattern');
    const pattern = new RegExp(legalCharacters);
    for (let i = 0; i < text.length; i++) {
        const character = text.charAt(i);
        if (!character.match(pattern)) {
            evt.preventDefault();
            return false;
        }
    }
};
