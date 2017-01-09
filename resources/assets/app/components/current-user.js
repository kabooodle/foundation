module 'currentUser' {
    export default function() {
        return KABOOODLE_APP && KABOOODLE_APP.currentUser;
    }
}