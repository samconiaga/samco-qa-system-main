import { useTranslation } from "react-i18next";
import TextInput from "../../../../src/components/ui/TextInput";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";
import { use, useEffect, useState } from "react";
import Button from "../../../../src/components/ui/Button";
import ErrorInput from "../../../../src/components/ui/ErrorInput";
import TextAreaInput from "../../../../src/components/ui/TextAreaInput";

export default function ImpactRiskAssesmentForm({ data, setData, errors, setError, clearErrors, ...props }) {
    const { t } = useTranslation();

    const [rpn, setRpn] = useState(0);

    const handleChange = (field, value) => {
        setData(field, value);

        if (errors[field]) {
            clearErrors(field);
            clearErrors('rpn');
            clearErrors('risk_category');
        }
    };

    useEffect(() => {
        const { severity, probability, detectability } = data;

        if (severity && probability && detectability) {
            const calculatedRpn = severity * probability * detectability;
            setRpn(calculatedRpn);
            setData("rpn", calculatedRpn);

            const ranges = [
                { min: 1, max: 15, category: "Minor" },
                { min: 16, max: 30, category: "Major" },
                { min: 31, max: 60, category: "Critical" },
            ];
            const categoryObj = ranges.find(
                (r) => calculatedRpn >= r.min && calculatedRpn <= r.max
            );
            const category = categoryObj ? categoryObj.category : "";
            setData("risk_category", category);
        }
    }, [data.severity, data.probability, data.detectability]);
    return (
        <>
            <div className='row gy-3'>
                <div className='col-sm-6'>
                    <label className='form-label'>{t('source_of_risk')}</label>

                    <TextAreaInput
                        autoComplete="off"
                        value={data.source_of_risk}
                        onChange={(e) => setData('source_of_risk', e.target.value)}
                        placeholder={t('enter_attribute', { 'attribute': t('source_of_risk') })}
                        errorMessage={errors.source_of_risk}
                    />
                </div>
                <div className='col-sm-6'>
                    <label className='form-label'>{t('impact_of_risk')}</label>
                    <TextAreaInput
                        autoComplete="off"
                        value={data.impact_of_risk}
                        onChange={(e) => setData('impact_of_risk', e.target.value)}
                        placeholder={t('enter_attribute', { 'attribute': t('impact_of_risk') })}
                        errorMessage={errors.impact_of_risk}
                    />
                </div>
                <hr />
                {/* Severity */}
                <div className="col-sm-4">
                    <label className="form-label">
                        {`${t("risk_evaluation_criteria")} (Severity)`}
                    </label>
                    <div className="form-check style-check d-flex align-items-center">
                        {[1, 2, 3, 4].map((val) => (
                            <CheckBoxInput
                                key={`severity_${val}`}
                                type="radio"
                                id={`severity_${val}`}
                                label={val.toString()}
                                errorMessage={errors.severity}
                                errorInput={!!errors.severity}
                                className="checked-danger border border-neutral-300"
                                onChange={(e) => handleChange("severity", parseInt(e.target.value))}
                                checked={data.severity == val}
                                value={val}
                            />
                        ))}
                    </div>
                </div>

                {/* Probability */}
                <div className="col-sm-4">
                    <label className="form-label">
                        {`${t("risk_evaluation_criteria")} (Probability)`}
                    </label>
                    <div className="form-check style-check d-flex align-items-center">
                        {[1, 2, 3, 4, 5].map((val) => (
                            <CheckBoxInput
                                key={`probability_${val}`}
                                type="radio"
                                id={`probability_${val}`}
                                label={val.toString()}
                                errorMessage={errors.probability}
                                errorInput={!!errors.probability}
                                className="checked-danger border border-neutral-300"
                                onChange={(e) => handleChange("probability", parseInt(e.target.value))}
                                checked={data.probability == val}
                                value={val}
                            />
                        ))}
                    </div>
                </div>
                {/* Detectability */}
                <div className="col-sm-4">
                    <label className="form-label">
                        {`${t("risk_evaluation_criteria")} (Detectability)`}
                    </label>
                    <div className="form-check style-check d-flex align-items-center">
                        {[1, 2, 3].map((val) => (
                            <CheckBoxInput
                                key={`detectability_${val}`}
                                type="radio"
                                id={`detectability_${val}`}
                                label={val.toString()}
                                errorMessage={errors.detectability}
                                errorInput={!!errors.detectability}
                                className="checked-danger border border-neutral-300"
                                onChange={(e) => handleChange("detectability", parseInt(e.target.value))}
                                checked={data.detectability == val}
                                value={val}
                            />
                        ))}
                    </div>
                </div>
                {(errors.detectability || errors.severity || errors.probability) && (
                    <ErrorInput
                        message={errors.detectability || errors.severity || errors.probability}
                        className="text-danger"
                    />
                )}


                <hr />

                <div className='col-sm-6'>
                    <label className='form-label'>{t('cause_of_risk')}</label>
                    <TextAreaInput
                        autoComplete="off"
                        value={data.cause_of_risk}
                        onChange={(e) => setData('cause_of_risk', e.target.value)}
                        placeholder={t('enter_attribute', { 'attribute': t('cause_of_risk') })}
                        errorMessage={errors.cause_of_risk}
                    />
                </div>
                <div className='col-sm-6'>
                    <label className='form-label'>{t('control_implemented')}</label>
                    <TextAreaInput
                        autoComplete="off"
                        value={data.control_implemented}
                        onChange={(e) => setData('control_implemented', e.target.value)}
                        placeholder={t('enter_attribute', { 'attribute': t('control_implemented') })}
                        errorMessage={errors.control_implemented}
                    />
                </div>
                <hr />
                <div className='col-sm-6'>
                    <label className='form-label'>{t('risk_priority_number')}</label>
                    <TextInput
                        className="form-control"
                        autoComplete="off"
                        value={rpn}
                        onChange={(e) => setData('rpn', e.target.value)}
                        placeholder={t('enter_attribute', { 'attribute': t('rpn') })}
                        errorMessage={errors.rpn}
                        readOnly
                    />
                </div>
                <div className='col-sm-6'>
                    <label className='form-label'>{t('risk_category')}</label>
                    <TextInput
                        className="form-control"
                        autoComplete="off"
                        value={data.risk_category}
                        onChange={(e) => setData('risk_category', e.target.value)}
                        placeholder={t('enter_attribute', { 'attribute': t('risk_category') })}
                        errorMessage={errors.risk_category}
                        readOnly
                    />
                </div>
            </div>
        </>
    );
}
