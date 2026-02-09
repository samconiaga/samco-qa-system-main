import { useTranslation } from "react-i18next";

export default function IssueResolutionFileDetail({ issue }) {
    const { t } = useTranslation();
    return (
        <>
            <div
                className="tab-pane fade show container"
                id="issue-resolution-file-detail"
                role="tabpanel"
                aria-labelledby="issue-resolution-file-detail-tab"
                tabIndex={0}
            >
                <div className="table-responsive">
                    <table className="table table-bordered">
                        <tbody>
                            {issue?.issueResolutionFileUrls?.map((url, index) => (
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
            </div>
        </>
    )
}