import os
import re

VIEWS_DIR = r'c:\laragon\www\ojs-system\resources\views'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    
    # We will process line by line or by using a regex on the class attribute
    # A class attribute looks like class="..."
    def class_replacer(match):
        class_content = match.group(1)
        
        has_fu = bool(re.search(r'\bfu\b', class_content))
        
        delay_match = re.search(r'\bfd(\d+)\b', class_content)
        delay_val = None
        if delay_match:
            delay_val = delay_match.group(1)
            
        if not has_fu and not delay_val:
            return match.group(0) # no change
            
        # Clean up the class content
        class_content = re.sub(r'\bfu\b', '', class_content)
        class_content = re.sub(r'\bfd\d+\b', '', class_content)
        # clean extra spaces
        class_content = re.sub(r'\s+', ' ', class_content).strip()
        
        new_attr = f'class="{class_content}"'
        if has_fu:
            new_attr += ' data-aos="fade-up"'
        if delay_val:
            # fd1 -> 100, fd2 -> 200, etc.
            # wait, earlier in author/dashboard.blade.php I did fd{{ $loop->index+1 }}
            # For dynamic ones like fd{{ $loop->index+1 }}, the regex \bfd(\d+)\b won't match the dynamic part.
            # Let's match fd\d+ and dynamic ones.
            pass
            
        return new_attr

    # Handle static ones
    # Match class="..."
    # We use a simple approach: just replace ' fu ' with ' ' and add data-aos="fade-up" at the end of the tag?
    # No, that's risky. 
    pass

# A simpler robust regex replacement:
# 1. find ` fu"` -> `" data-aos="fade-up"`
# 2. find ` fu ` -> ` ` and append data-aos... actually, just replace class="... fu ..." with class="..." data-aos="fade-up"

def process_file_v2(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    orig = content
    
    # Find all class="..."
    def repl(m):
        cls = m.group(1)
        has_fu = re.search(r'\bfu\b', cls)
        
        # for delay, it could be fd1, fd2, or fd{{ $loop->index+1 }}
        # let's extract it
        delay_str = None
        
        # static delay: fd1, fd2...
        m_fd = re.search(r'\bfd(\d+)\b', cls)
        if m_fd:
            delay_str = m_fd.group(1) + "00"
            cls = re.sub(r'\bfd\d+\b', '', cls)
            
        # dynamic delay: fd{{ $loop->index+1 }}
        m_dyn = re.search(r'\bfd(\{\{.+?\}\})\b', cls)
        if m_dyn:
            # e.g. {{ $loop->index+1 }} -> we can't easily multiply by 100 inside blade without changing the php expression.
            # Actually, `data-aos-delay="{{ 100 + ($loop->index * 100) }}"`
            # Just ignore dynamic ones or handle specifically. 
            pass

        if not has_fu and not delay_str:
            return m.group(0)
            
        cls = re.sub(r'\bfu\b', '', cls)
        cls = re.sub(r'\s+', ' ', cls).strip()
        
        res = f'class="{cls}"'
        if has_fu:
            res += ' data-aos="fade-up"'
        if delay_str:
            res += f' data-aos-delay="{delay_str}"'
            
        return res

    content = re.sub(r'class="([^"]+)"', repl, content)
    
    if content != orig:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    return False

modified = 0
for root, dirs, files in os.walk(VIEWS_DIR):
    for file in files:
        if file.endswith('.blade.php'):
            path = os.path.join(root, file)
            # Skip the ones we already did manually
            if 'dashboard.blade.php' in file:
                continue
            if process_file_v2(path):
                modified += 1
                
print(f"Modified {modified} files.")
