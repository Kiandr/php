.task-content .actions {
    display: flex;
    flex-wrap: wrap;
    margin: 0 !important;
}

@media (max-width: 480px) {
    .task-content .btn {
        width: 100%;
        display: block;
    }

    .task-content li {
        width: 100%;
        margin-right: 0;
    }

    .task-action-form .btn {
        width: 100%;
    }

    .task-action-form .btn:not(:last-of-type) {
        margin-bottom: 8px;
    }
}
