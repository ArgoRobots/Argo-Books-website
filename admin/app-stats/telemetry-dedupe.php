<?php
/**
 * Duplicate-upload suppression for desktop telemetry events.
 *
 * The app only marks an event as uploaded once the server has answered, and it
 * persists that flag to a device-global file that several running instances share.
 * Either half of that can be lost: a response that never arrives, or an instance
 * writing a stale copy of the file over an instance that had just recorded the
 * flag. When it is lost the same events are sent again on the next flush, and the
 * server writes them to a second file, because upload.php stores whatever arrives.
 *
 * So the same event legitimately exists in more than one file on disk, and every
 * read site has to collapse them or it reports one action several times over. The
 * symptom is always a run of identical timestamps: the timestamp is stamped once
 * when the event is created and never changes, however many times it is uploaded.
 *
 * Events carry a dataId for exactly this purpose. It is scoped per device rather
 * than trusted globally, so two devices whose ids ever collided cannot suppress
 * each other's activity. Events from builds too old to send a dataId cannot be
 * collapsed and are all kept, which overcounts those but never drops anything.
 */

/**
 * Returns true when this event has already been counted for this device, and
 * records it as seen otherwise.
 *
 * @param array  $event  A single event from an uploaded payload.
 * @param string $scope  Per-device key, normally the file's authId.
 * @param array  $seen   Caller-owned set, passed by reference across every file.
 */
function telemetry_is_duplicate_event(array $event, string $scope, array &$seen): bool
{
    // The compact upload format wraps the payload one level down.
    if (isset($event['event']) && is_array($event['event'])) {
        $event = $event['event'];
    }

    $dataId = $event['dataId'] ?? null;
    if (!is_string($dataId) || $dataId === '') {
        return false;
    }

    $key = $scope . '|' . $dataId;
    if (isset($seen[$key])) {
        return true;
    }

    $seen[$key] = true;
    return false;
}
