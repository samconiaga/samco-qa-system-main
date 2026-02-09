import React from "react";
import '../../../css/kop.css'

const Kop = () => {
    return (
        <div className="kop">
            <table className="kop-table">
                <thead>
                    <tr>
                        <td className="kop-logo">
                            <img
                                src="/assets/images/logo.png"
                                alt="Logo SAMCO"
                                className="kop-logo-img"
                            />
                        </td>
                        <td className="kop-text">
                            <span className="kop-title">PT. SAMCO FARMA</span>
                            <span className="kop-subtitle">
                                (PHARMACEUTICAL & CHEMICAL INDUSTRIES)
                            </span>
                            <p className="kop-address">
                                Jl. Jend Gatot Subroto Km. 1,2 No. 27 <br />
                                Cibodas – Tangerang, Banten 15138
                            </p>
                            <p className="kop-contact">
                                Telp. : (021) 5525810/30, 5524084 Fax. : (021)
                                5537097 <br />
                                Website:{" "}
                                <a
                                    href="http://www.samcofarma.co.id"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    www.samcofarma.co.id
                                </a>{" "}
                                Email:{" "}
                                <a href="mailto:cs@samcofarma.co.id">
                                    cs@samcofarma.co.id
                                </a>
                            </p>
                        </td>
                        <td className="kop-cert">
                            <img
                                src="/assets/images/ukas.jpg"
                                alt="Cert"
                                className="kop-cert-img"
                            />
                           
                        </td>
                    </tr>
                </thead>
            </table>
            <div className="kop-border"></div>
        </div>
    );
};

export default Kop;
