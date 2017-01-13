export default function() {
    return (KABOOODLE_APP && KABOOODLE_APP.currentUser) ? KABOOODLE_APP.currentUser : false;
}