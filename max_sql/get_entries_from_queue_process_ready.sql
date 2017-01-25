select queue.id, handle, process_id, status, retries, queue.time_created from queue,scheduledprocess where type='Process' and status='Ready' and retries=1 and object_id=scheduledprocess.id;
