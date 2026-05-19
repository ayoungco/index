import csv
import uuid
from datetime import datetime

BASE_URL = "https://index.ayoung.co/"

# how many labels you want to generate
COUNT = 50 
OUTPUT_FILE = f"index-labels-{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"

with open(OUTPUT_FILE, "w", newline="") as f:
    writer = csv.writer(f)
    writer.writerow(["url"])  # header

    for _ in range(COUNT):
        unique_id = str(uuid.uuid4())
        writer.writerow([BASE_URL + unique_id])

print(f"Generated {COUNT} URLs in {OUTPUT_FILE}")