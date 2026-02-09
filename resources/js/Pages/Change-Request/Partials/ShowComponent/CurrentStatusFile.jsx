import { useTranslation } from "react-i18next";

export default function CurrentStatusFile({ currentStatusFile }) {
    const { t } = useTranslation();

    return (
        <div className="table-responsive">
            <table className="table table-bordered">
                <tbody>
                    {currentStatusFile?.map((url, index) => (
                        <tr key={`file-${index}`}>
                            <th>{`${t('file')} ${index + 1}`}</th>
                            <td>
                                <a
                                    href={url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-decoration-underline text-primary"
                                >
                                    {t('view_attachment')}
                                </a>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
