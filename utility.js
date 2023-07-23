import fs from 'fs/promises'

export async function exists(filename) {
    try{
        await fs.stat(filename)
        return true;
    } catch {
        return false;
    }
}
