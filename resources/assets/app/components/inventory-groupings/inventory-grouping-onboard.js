import onboard from '../onboard';

export default function() {
    let intro = onboard();

    let steps = [
        {
            element: document.querySelectorAll('.identifier-section')[0],
            intro: "Name and describe your outfit here. In order for you to identify one outfit from another, the outfit name needs to be unique.",
            step: 0,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.price-section')[0],
            intro: "How much will your outfit cost? Put that information here. To help you out, as you add inventory items, the Accum Wholesale Price will change to show you what the wholesale price of the outfit is.",
            step: 1,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.auto-add-section')[0],
            intro: "Want the outfit price to simply be the price of the included inventory items added together? No problem. Keep this toggled on, and we'll do the math for you.",
            step: 2,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.quantity-section')[0],
            intro: "How many of this outfit do you want to have? Put that information here. To help you out, we will warn you when the included inventory items can't satisfy the amount you put here.",
            step: 3,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.max-quantity-section')[0],
            intro: "Want the outfit quantity to simply be the maximum quantity allowed based on the availablity of the included inventory items? No problem. Keep this toggled on, and we'll keep that figured out for you.",
            step: 4,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.inventory-section')[0],
            intro: "The outfit's associated inventory items will display here. Once associated you can remove them from here as well if you want.",
            step: 5,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.locked-section')[0],
            intro: "Want to make sure you always have the included inventory items on hand to satify claims on the outfit? No problem. Keep this toggled on, and we will make sure to keep the quantity of the outfit set aside for each included inventory item.",
            step: 6,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.image-section')[0],
            intro: "You love the way your outfit looks. Add an image of it here to help identify the outfit and to present it others.",
            step: 7,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelectorAll('.available-inventory-section')[0],
            intro: "These are all the available inventory items you can include in your outfit. Click items to add and remove them from the outfit.",
            step: 8,
            position: 'top',
        },
    ];

    intro.addSteps(steps);

    return intro;
}
