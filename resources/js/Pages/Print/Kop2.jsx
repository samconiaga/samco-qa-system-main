import React from "react";
import "../../../css/kop2.css";
const Kop2 = () => {
  return (
    <div className="kop-wrapper">
      <table className="kop-table">
        <tbody>
          <tr>
            <td className="kop-left">
              <img src="/assets/images/logo.png" alt="Logo" />
              <div className="kop-company">
                PT. SAMCO FARMA
              </div>
            </td>

            <td className="kop-right">
              <div className="kop-title">
                PENGENDALIAN PERUBAHAN
              </div>
              <div className="kop-subtitle">
                (CHANGE CONTROL)
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  );
};

export default Kop2;
