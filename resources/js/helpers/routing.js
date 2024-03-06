import {flattenObjectAsArray} from "@/helpers/data";
import routes from "@/router/routes";

export const toUrl = function (page) {
    if (page.charAt(0) === '/') {
        page = page.substring(1);
    }
    return `/panel/${page}`;
}
