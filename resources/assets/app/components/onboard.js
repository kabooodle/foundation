import introJs from 'intro.js';

export default function() {
    let intro = introJs.introJs();
    intro.setOption('tooltipPosition', 'bottom-middle-aligned');

    $Bus.$on('tour:finished', ()=>{
        $(document).find('.onboard-show-btn').show();
    });

    intro.onchange(function (newStep) {
        $Bus.$emit('tour:changed', this._currentStep, newStep);
    });
    intro.onexit(function() {
        $Bus.$emit('tour:finished');
    });
    intro.oncomplete(function() {
        $Bus.$emit('tour:finished');
    });

    return intro;
}