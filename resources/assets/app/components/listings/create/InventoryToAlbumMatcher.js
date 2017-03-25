/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import Fuse from 'fuse.js';

class InventoryToAlbumMatcher {

    /**
     *
     * @param {array} haystack
     * @param {array} needles
     */
    constructor (haystack, needles){
        this._haystack = haystack;
        this._needles = needles;
        this._matches = [];
        this._misses = [];
        this._matchresults = [];
    }

    /**
     *
     * @returns {number}
     */
    static scoreVariance(){
        return 86;
    }

    /**
     *
     * @returns {{include: [string], shouldSort: boolean, threshold: number, location: number, distance: number, maxPatternLength: number, minMatchCharLength: number, keys: [*,*]}}
     */
    static fuseOptions(){
        return {
            include: ["score"],
            shouldSort: true,
            threshold: 0.4,
            location: 0,
            distance: 0,
            maxPatternLength: 100,
            minMatchCharLength: 3,
            keys: ["name"],
        };
    }

    /**
     *
     * @returns {{album_id: null, album: null, listable_id: null, listable: {}, match: null, match_score: null, needle: null}}
     */
    static searchMatchResult(){
        return {
            album_id: null,
            album: null,
            listable_id: null,
            listable: {},
            match: null,
            match_score: null,
            needle: null,
        }
    };

    /**
     *
     * @returns {array|*}
     */
    haystack(){
        return this._haystack;
    }

    /**
     *
     * @returns {array|*}
     */
    needles(){
        return this._needles;
    }

    /**
     *
     * @returns {Array}
     */
    matches(){
        return this._matches;
    }

    /**
     *
     * @returns {Array}
     */
    misses(){
        return this._misses;
    }

    /**
     *
     * @returns {Array|*}
     */
    matchResults(){
        return this._matchresults;
    }

    /**
     *
     * @param {string} needle
     * @returns {string}
     */
    prepareNeedle(needle){
        return needle.toLowerCase().replace(' - ', ' ').trim();
    }

    /**
     *
     * @param {number} score
     * @returns {number}
     */
    normalizeScore(score){
        return (1 - score) * 100;
    }

    /**
     *
     * @param {number} score
     * @returns {boolean}
     */
    satisfiesMinScore(score){
        return this.normalizeScore(score) >= this.constructor.scoreVariance();
    }

    /**
     *
     * @param {number} score
     * @returns {boolean}
     */
    satisfiesPerfectScore(score){
        return this.normalizeScore(score) == 100;
    }

    performSearch(){
        return new Promise((resolve)=>{
            let fuse = new Fuse(this.haystack(), this.constructor.fuseOptions());
            for (let i = 0; i < this.needles().length; i++) {
                let needle = this.needles()[i];
                this.performNeedleSearch(fuse, needle);
            }
            resolve();
        });
    }

    performNeedleSearch(fuse, needle){
        // Convert VUE object to plain object so we dont have stupid nested listeners
        let temp_listable = JSON.parse(JSON.stringify(needle));

        // Build the needle we are looking for in the haystack, or rather,
        // string we are searching for in the array of possibilities.
        let name = this.prepareNeedle(needle.name);
        let name_alt = this.prepareNeedle(needle.name_alt);

        // first search param is by name_alt;
        let search_param = name_alt;

        // Search through the haystack, comparing our needle, and return array of all possible matches
        let found_results = fuse.search(search_param);

        // Glass is half empty; Start with having no match;
        let match = false;

        // Our ideal match will always be the first key in the array of possible matches
        let ideal_match = found_results[0];

        // If we have an ideal match, we further qualify the match based on the score.
        // If its >= our MIN_SCORE, we will consider this match as our matching album.
        if (ideal_match && this.satisfiesPerfectScore(ideal_match.score)) {
            match = ideal_match;
        } else {
            // No ideal match, so lets search now using a different needle, just to be sure.
            found_results = fuse.search(name);
            let style_based_ideal_match = found_results[0];

            if (style_based_ideal_match && this.satisfiesMinScore(style_based_ideal_match.score)){
                match = style_based_ideal_match;
            }
        }

        // Result object
        let result = this.constructor.searchMatchResult();
        result.album_id = match ? match.item.id : null;
        result.album = match ? match.item : null;
        result.listable_id = temp_listable.id;
        result.listable = temp_listable;
        result.match = match ? match : null;
        result.match_score = match ? this.normalizeScore(match.score) : null;
        result.needle = needle;

        if (match) {
            const index = _.findIndex(this._matchresults, {key: result.album_id});
            if (index == -1) {
                this._matchresults.push({key: result.album_id, results: [result]});
            } else {
                this._matchresults[index].results.push(result);
            }

            this._matches.push(temp_listable);
        } else {
            this._misses.push(temp_listable);
        }
    }
}

export default InventoryToAlbumMatcher;