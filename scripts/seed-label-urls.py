import csv
import uuid

BASE_URL = "https://index.ayoung.co/"
COUNT = 50             # how many labels you want to generate
OUTPUT_FILE = "labels.csv"

with open(OUTPUT_FILE, "w", newline="") as f:
    writer = csv.writer(f)
    writer.writerow(["url"])  # header

    for _ in range(COUNT):
        unique_id = str(uuid.uuid4())
        writer.writerow([BASE_URL + unique_id])

print(f"Generated {COUNT} URLs in {OUTPUT_FILE}")