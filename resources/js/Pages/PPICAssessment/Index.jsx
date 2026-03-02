import React, { useState } from "react";
import Form from "./Form";

export default function Index(props) {
    return (
        <React.Fragment>
            <Form {...props} />
        </React.Fragment>
    );
}
