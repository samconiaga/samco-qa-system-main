import { useTranslation } from "react-i18next";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";



export default function SelectDepartmentForm({ data, setData, errors, departments }) {
    const { t } = useTranslation();
    const midpoint = Math.ceil(departments.length / 2);
    const leftColumn = departments.slice(0, midpoint);
    const rightColumn = departments.slice(midpoint);

    return (
        <>
            <div className='row gy-3'>
                <div className='col-sm-9'>
                    <label className="form-label">{t('select_related_departments')}</label>
                    <div className="row">

                        <div className="col-md-6">
                            {leftColumn.map((department) => {
                                const checked = data.related_departments.some(rd => rd.department_id === department.id);

                                return (
                                    <div key={department.id} className="form-check style-check d-flex align-items-center">
                                        <CheckBoxInput
                                            id={`department_${department.id}`}
                                            label={department.name}
                                            checked={checked}
                                            value={department.id}
                                            onChange={(e) => {
                                                setData('related_departments',
                                                    e.target.checked
                                                        ? [...(data.related_departments || []), { department_id: department.id }]
                                                        : (data.related_departments || []).filter(ap => ap?.department_id !== department.id)
                                                );
                                            }}
                                        />
                                    </div>
                                );
                            })}
                        </div>

                        <div className="col-md-6">
                            {rightColumn.map((department) => {
                                const checked = data.related_departments.some(rd => rd.department_id === department.id);

                                return (
                                    <div key={department.id} className="form-check style-check d-flex align-items-center">
                                        <CheckBoxInput
                                            id={`department_${department.id}`}
                                            label={department.name}
                                            checked={checked}
                                            value={department.id}
                                            onChange={(e) => {
                                                setData('related_departments',
                                                    e.target.checked
                                                        ? [...(data.related_departments || []), { department_id: department.id }]
                                                        : (data.related_departments || []).filter(ap => ap?.department_id !== department.id)
                                                );
                                            }}
                                        />
                                    </div>
                                );
                            })}
                        </div>

                    </div>

                </div>
            </div >
        </>
    );
}
