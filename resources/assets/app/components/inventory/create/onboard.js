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
            intro: "Select a size, add images, and categories.",
            step: 2,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.sizing-row')[0],
            intro: "A size must be selected.",
            step: 3,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.add-images-btn')[0],
            intro: "Each image represents 1 item, but you can increment the quantity under each photo.",
            step: 4,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.add-categories-btn')[0],
            intro: "Categories can be anything you want - think 'keywords'",
            step: 5,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelector('#size-add-btn'),
            intro: "To add another size, simple click 'Add Size'",
            step: 6,
            position: 'bottom-middle-aligned',
        },
    ];

    intro.addSteps(steps);

    return intro;
}