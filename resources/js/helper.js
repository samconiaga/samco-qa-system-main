import React from "react";
import i18n from "./src/i18n";
import DOMPurify from "dompurify";
// Bandingkan 2 objek File berdasarkan name + size
function isSameFile(fileA, fileB) {
    if (!fileA && !fileB) return true;
    if (!fileA || !fileB) return false;
    return fileA.name === fileB.name && fileA.size === fileB.size;
}

// Bandingkan 2 array File
function isSameFileList(listA = [], listB = []) {
    if (listA.length !== listB.length) return false;
    return listA.every((file, i) => isSameFile(file, listB[i]));
}
function toDateString(date, showWeekday = true) {
    if (!date) return "-";
    const d = new Date(date);
    const locale = i18n.language;

    const options = {
        year: "numeric",
        month: "short",
        day: "numeric",
    };

    if (showWeekday) {
        options.weekday = "long";
    }

    return d.toLocaleDateString(locale, options);
}

function toDateTimeString(date, hour12 = false, showWeekday = true) {
    if (!date) return "-";
    const d = new Date(date);
    const locale = i18n.language;
    const options = {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12,
    };
    if (showWeekday) {
        options.weekday = "long";
    }
    return d.toLocaleString(locale, options);
}

function stripHtml(html) {
    const cleanHtml = DOMPurify.sanitize(html);
    return React.createElement("div", {
        className: "prose max-w-none",
        dangerouslySetInnerHTML: { __html: cleanHtml },
    });
}
export {
    isSameFile,
    isSameFileList,
    toDateString,
    stripHtml,
    toDateTimeString,
};
