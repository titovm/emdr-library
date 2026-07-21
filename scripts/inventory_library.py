from __future__ import annotations

import hashlib
import json
import os
import re
import subprocess
import sys
import zipfile
from pathlib import Path

ROOT = Path(sys.argv[1])
OUT = Path(sys.argv[2])


def clean(text: str) -> str:
    text = text.replace("\x00", " ")
    return re.sub(r"\s+", " ", text).strip()


def xml_text(path: Path, members: list[str]) -> str:
    chunks: list[str] = []
    try:
        with zipfile.ZipFile(path) as archive:
            for member in members:
                try:
                    raw = archive.read(member).decode("utf-8", "ignore")
                except KeyError:
                    continue
                chunks.extend(re.findall(r"<a:t>(.*?)</a:t>|<w:t[^>]*>(.*?)</w:t>", raw, re.S))
    except Exception:
        return ""
    flattened = [a or b for a, b in chunks]
    return clean(" ".join(flattened))


def extract_text(path: Path) -> str:
    suffix = path.suffix.lower()
    try:
        if suffix == ".pdf":
            proc = subprocess.run(
                ["pdftotext", "-f", "1", "-l", "2", "-layout", str(path), "-"],
                capture_output=True,
                text=True,
                timeout=20,
            )
            return clean(proc.stdout)[:3000]
        if suffix == ".docx":
            return xml_text(path, ["word/document.xml"])[:3000]
        if suffix == ".pptx":
            return xml_text(path, [f"ppt/slides/slide{i}.xml" for i in range(1, 4)])[:3000]
        if suffix in {".txt"}:
            return clean(path.read_text(errors="ignore"))[:3000]
        if suffix in {".doc", ".ppt", ".pps"}:
            proc = subprocess.run(["strings", "-el", str(path)], capture_output=True, text=True, timeout=20)
            return clean(proc.stdout)[:3000]
    except Exception:
        return ""
    return ""


records = []
for path in sorted((p for p in ROOT.rglob("*") if p.is_file()), key=lambda p: str(p).casefold()):
    print(path.relative_to(ROOT), flush=True)
    rel = path.relative_to(ROOT)
    stat = path.stat()
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        if stat.st_size <= 50 * 1024 * 1024:
            for chunk in iter(lambda: handle.read(1024 * 1024), b""):
                digest.update(chunk)
        else:
            digest.update((path.name + str(stat.st_size)).encode())
    records.append({
        "relative_path": str(rel),
        "filename": path.name,
        "extension": path.suffix.lower().lstrip("."),
        "size_bytes": stat.st_size,
        "sha256": digest.hexdigest(),
        "text": "" if os.environ.get("SKIP_TEXT") == "1" else extract_text(path),
    })

OUT.parent.mkdir(parents=True, exist_ok=True)
OUT.write_text(json.dumps(records, ensure_ascii=False, indent=2), encoding="utf-8")
print(json.dumps({"files": len(records), "bytes": sum(r["size_bytes"] for r in records)}, ensure_ascii=False))
