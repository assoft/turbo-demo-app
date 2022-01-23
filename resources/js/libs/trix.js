import Trix from 'trix';

Trix.config.blockAttributes.heading2 = {
    tagName: "h2",
    terminal: true,
    breakOnReturn: true,
    group: false
}

Trix.config.blockAttributes.heading3 = {
    tagName: "h3",
    terminal: true,
    breakOnReturn: true,
    group: false
}

window.Trix = Trix;

export default Trix;
