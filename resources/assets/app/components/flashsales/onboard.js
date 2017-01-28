import onboard from '../onboard';

export default function() {
    let intro = onboard();

    let steps = [
        {
            element: document.querySelector('#create_flashsale_container'),
            intro: "Create a flash sale, and determine various options regarding the sale.",
            step: 1,
            position: 'bottom-middle-aligned',
        },
        {
            intro: "Invite sellers and optionally assign each group of sellers a time slot to list their inventory.",
            step: 2,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelector('#btn-create-sellergroup'),
            intro: "Create groups of sellers anytime, as many as you want here.",
            step: 3,
            position: 'bottom-middle-aligned',
        },
        {
            element: document.querySelector('#btn-add-sellersgroup'),
            intro: "Add the groups of sellers you created to the sale.",
            step: 4,
            position: 'bottom-middle-aligned',
        },
        {
            intro: "Once a flash sale is created, sellers will be notified immediately. Let the fun begin!",
            step: 5,
            position: 'bottom-middle-aligned',
        },
    ];

    intro.addSteps(steps);

    return intro;
}