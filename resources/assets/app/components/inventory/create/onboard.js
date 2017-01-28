import onboard from '../../onboard';

export default function() {
    let intro = onboard();

    let steps = [
        {
            element: document.querySelector('#addItemsPrimaryContainer'),
            intro: "Make basic selections about the type of inventory item(s) you are adding here.",
            step: 1,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.sizing_container')[0],
            intro: "Hello world!",
            step: 2,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.sizing-row')[0],
            intro: "Hello world!",
            step: 3,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.add-images-btn')[0],
            intro: "Hello world!",
            step: 4,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.add-categories-btn')[0],
            intro: "Hello world!",
            step: 5,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelector('#size-add-btn'),
            intro: "Hello world!",
            step: 6,
            position: 'bottom-middle-aligned',
        },
    ];

    intro.addSteps(steps);

    return intro;
}