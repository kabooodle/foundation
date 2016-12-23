
new Vue({
    el: '#watching_items',

    methods: {
        deleteWatching(event){
            event.preventDefault();
            const form = event.target;
            let submitEl = form.getElementsByClassName('btn-action-del')[0];
            let endpoint = form.getAttribute('action');
            let container = form.closest('tr');

            submitEl.classList.add('disabled');
            submitEl.disabled = true;
            submitEl.innerHTML = submitEl.innerHTML + (spinny());

            this.$http.delete(endpoint).then(response=>container.parentNode.removeChild(container));
        },
    }
});